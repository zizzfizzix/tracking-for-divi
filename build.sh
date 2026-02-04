#!/bin/bash
set -e

pkgname=tracking-for-divi

# Redirect all output to a log file
mkdir -p ./build_logs
exec > >(tee "./build_logs/build.$(date +%Y-%m-%d-%H-%M-%S).log") 2>&1

package() {
  if [[ -e ./${pkgname}.zip ]]; then
    rm ./${pkgname}.zip
  fi

  case $1 in
  production)
    composer install --no-dev
    composer dump-autoload -o --no-dev
    ;;
  develop)
    composer install
    composer dump-autoload -o
    ;;
  esac

  npm install
  npm run build

  wp i18n make-mo ./languages

  # Create dist directory and copy files using .distignore
  rm -rf ./dist
  mkdir -p ./dist/${pkgname}

  rsync -av --exclude-from='.distignore' . ./dist/${pkgname}/

  # For develop builds, keep .po files for translation work
  if [[ $1 == "develop" ]]; then
    cp ./languages/*.po ./dist/${pkgname}/languages/ 2>/dev/null || true
  fi

  # Create the zip
  cd ./dist && zip -r ../${pkgname}.zip ${pkgname}
  cd ..
  rm -rf ./dist
}

deploy() {
  case $1 in
  production)
    package production
    ;;
  develop)
    package develop
    ;;
  esac

  docker_data_dir=./docker/data

  mkdir -p ${docker_data_dir}

  unzip -d ${docker_data_dir}/ ./${pkgname}.zip

  if [[ -d ${docker_data_dir}/wp-content/plugins/${pkgname} ]]; then
    rm -r ${docker_data_dir}/wp-content/plugins/${pkgname}
  fi

  cp -R ${docker_data_dir}/${pkgname} ${docker_data_dir}/wp-content/plugins/

  rm -r ${docker_data_dir}/${pkgname}
  rm ./${pkgname}.zip
}

case $1 in

package-production)
  package production
  ;;

package-develop)
  package develop
  ;;

deploy-production)
  deploy production
  ;;

deploy-develop)
  deploy develop
  ;;

*)
  echo Use package-production, package-develop, deploy-production or deploy-develop as an argument.
  ;;

esac

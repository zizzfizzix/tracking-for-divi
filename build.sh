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
  pushd ..

  case $1 in
  production)
    zip -r ${pkgname}/${pkgname}.zip ./${pkgname}/ \
      -x "*/.*" \
      -x "**/*.zip" \
      -x "*/docker/*" \
      -x "*/build_logs/*" \
      -x "*/*.sh" \
      -x "*/node_modules/*" \
      -x "*/js/client/*" \
      -x "*/js/admin/*" \
      -x "*/Gruntfile.js" \
      -x "*/vite.config.js" \
      -x "*/package*.json"
    ;;
  develop)
    zip -r ${pkgname}/${pkgname}.zip ./${pkgname}/ \
      -x "*/.*" \
      -x "**/*.zip" \
      -x "*/docker/*" \
      -x "*/build_logs/*" \
      -x "*/*.sh" \
      -x "*/node_modules/*" \
      -x "*/js/client/*" \
      -x "*/js/admin/*" \
      -x "*/Gruntfile.js" \
      -x "*/vite.config.js" \
      -x "*/package*.json"
    ;;
  esac

  popd
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

  if [[ -d "${docker_data_dir}/wp-content/plugins/${pkgname}" ]]; then
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

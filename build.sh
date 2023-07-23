#!/bin/bash
set -e

# Redirect all output to a log file
mkdir -p ./build_logs
exec > >(tee "./build_logs/build.$(date +%Y-%m-%d-%H-%M-%S).log") 2>&1

package() {
  if [[ -e ./tracking-for-divi.zip ]]; then
    rm ./tracking-for-divi.zip
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
      zip -r tracking-for-divi/tracking-for-divi.zip ./tracking-for-divi/ \
        -x "*/.*" \
        -x "**/*.zip" \
        -x "*/docker/*" \
        -x "*/build_logs/*" \
        -x "*/*.sh" \
        -x "*/node_modules/*" \
        -x "*/js/src/*" \
        -x "*/Gruntfile.js" \
        -x "*/vite.config.js" \
        -x "*/package*.json"
    ;;
    develop)
      zip -r tracking-for-divi/tracking-for-divi.zip ./tracking-for-divi/ \
        -x "*/.*" \
        -x "**/*.zip" \
        -x "*/docker/*" \
        -x "*/build_logs/*" \
        -x "*/*.sh" \
        -x "*/node_modules/*" \
        -x "*/js/src/*" \
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

  mkdir -p ./docker/data

  unzip -d ./docker/data/ ./tracking-for-divi.zip

  cp -R ./docker/data/tracking-for-divi "./docker/data/wp-content/plugins/"

  rm -r ./docker/data/tracking-for-divi
  rm ./tracking-for-divi.zip
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

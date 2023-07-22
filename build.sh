#!/bin/bash
set -e

# Redirect all output to a log file
mkdir -p ./build_logs
exec > >(tee "./build_logs/build.$(date +%Y-%m-%d-%H-%M-%S).log") 2>&1

package() {
  if [[ -e ./divi-form-tracking.zip ]]; then
    rm ./divi-form-tracking.zip
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
      zip -r divi-form-tracking/divi-form-tracking.zip ./divi-form-tracking/ \
        -x "*/.*" \
        -x "**/*.zip" \
        -x "*/docker/*" \
        -x "*/wp/*" \
        -x "*/build_logs/*" \
        -x "*/*.sh" \
        -x "*/node_modules/*" \
        -x "*/js/src/*" \
        -x "*/Gruntfile.js" \
        -x "*/vite.config.js" \
        -x "*/package*.json"
    ;;
    develop)
      zip -r divi-form-tracking/divi-form-tracking.zip ./divi-form-tracking/ \
        -x "*/.*" \
        -x "**/*.zip" \
        -x "*/docker/*" \
        -x "*/wp/*" \
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

  mkdir -p ./wp

  unzip -d ./wp/ ./divi-form-tracking.zip

  cp -R ./wp/divi-form-tracking "./wp/wp-content/plugins/"

  rm -r ./wp/divi-form-tracking
  rm ./divi-form-tracking.zip
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

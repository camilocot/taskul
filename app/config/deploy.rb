set :application, "taskul"
set :domain,      "#{application}.net"
set :deploy_to,   "/var/www/#{domain}"
set :app_path,    "app"

set :repository,  "git@bitbucket.org:camilocot/#{application}.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :db,        domain, :primary => true
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :use_sudo,       false
set  :keep_releases,  3
set  :use_composer,   true
set  :update_vendors, true
set  :shared_files,        ["app/config/parameters.yml","web/js/fos_js_routes.js", "web/css/errors.css"]
set  :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", app_path + "/sessions" ]
set  :dump_assetic_assets, true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

# Symfony2 2.1
# set  :shared_children,     [app_path + "/logs", web_path + "/uploads", app_path + "/logs"]
# before 'symfony:composer:update', 'symfony:copy_vendors'

namespace :symfony do
  desc "Copy vendors from previous release"
  task :copy_vendors, :except => { :no_release => true } do
    if Capistrano::CLI.ui.agree("Do you want to copy last release vendor dir then do composer install ?: (y/N)")
      capifony_pretty_print "--> Copying vendors from previous release"

      run "cp -a #{previous_release}/vendor #{latest_release}/"
      capifony_puts_ok
    end
  end
end

after "deploy", "symfony:clear_apc"

namespace :symfony do
  desc "Clear apc cache"
  task :clear_apc do
    capifony_pretty_print "--> Clear apc cache"
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} apc:clear -e #{symfony_env_prod}'"
    capifony_puts_ok
  end
end

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
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :use_sudo,       false
set  :keep_releases,  3
set  :use_composer,   true
set  :update_vendors, true
set  :shared_files,   ["app/config/parameters.yml"]
set  :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL
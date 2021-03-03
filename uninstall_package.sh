#!/bin/bash
#to make this work, run "chmod 755 uninstall_package.sh"
#change theme to your project name
cd themes/dhh && npm uninstall $1 $2

##TO RUN:
#./uninstall_package.sh <package_name> <--save || --save-dev>
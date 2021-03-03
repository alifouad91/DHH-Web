#!/bin/bash
#to make this work, run "chmod 755 run_setup.sh"
#change theme to your project name
open -a Terminal.app;
osascript <<END 
tell application "Terminal"
    do script "cd \"`pwd`\"$1/themes/dhh;npm install;npm audit fix;exit"
end tell
END

##TO RUN:
#./run_setup.sh
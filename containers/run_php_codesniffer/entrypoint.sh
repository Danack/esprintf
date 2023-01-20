#!/bin/sh -l

set -e

echo '---Installing dependencies---'
php ./composer.phar update

sh runCodeSniffer.sh

# https://help.github.com/en/actions/automating-your-workflow-with-github-actions/development-tools-for-github-actions#set-an-error-message-error
# echo "::error file=app.js,line=10,col=15::Something went wrong"

echo "---fin---"

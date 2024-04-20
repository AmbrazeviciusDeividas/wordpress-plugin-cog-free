#!/bin/bash

# Define the SVN directory
SVN_REPO_PATH="/Users/ambrazevicius/wpiron-plugins/cost-of-goods"

# Update the entire SVN repository
svn up $SVN_REPO_PATH

# Directly remove specific non-minified JS and CSS files
svn rm $SVN_REPO_PATH/trunk/admin/js/cost-of-goods-for-woocommerce-admin.js
svn rm $SVN_REPO_PATH/trunk/admin/css/cost-of-goods-for-woocommerce-admin.css
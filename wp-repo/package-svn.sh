#!/usr/bin/env php
<?php

$plugin_root = __DIR__ . '/../';
chdir($plugin_root);

// 1. get version number & package
preg_match('#Version:(.*)#', file_get_contents($plugin_root . 'food-truck.php'), $matches);
$version = trim($matches[1]);
$zip_of_updated_code = $argv[1]; // passed as first argument in cmd line

// 1b. confirm
echo "ZIP: $zip_of_updated_code\n";
echo "Version: \"$version\"\t..continue? (y/n)\n";
$get_continue = strtolower(trim( fgets( STDIN ))) == 'y';
if(!$get_continue) die;

// 2b. confirm changelog
echo "Updated Changelog in readme.txt? ..continue? (y/n)\n";
$get_continue = strtolower(trim( fgets( STDIN ))) == 'y';
if(!$get_continue) die;

// 2c. confirm git repo updated
$has_working_changes = exec("git status --porcelain");
if($has_working_changes) {
  die("Error: Looks like you have git working changes\n");
}

// 3. get wordpress name / pass
echo "Wordpress.org username:\n";
$user = trim( fgets( STDIN ));
echo "Wordpress.org password:\n";
$pass = trim( fgets( STDIN ));

// 3. create svn repo directory
echo "Downloading WP SVN Repo...\n";
$tmp_repo_dir = "../_tmp-svn-repo-food-truck-wp-plugin";
exec("mkdir $tmp_repo_dir");
exec("svn co https://plugins.svn.wordpress.org/food-truck $tmp_repo_dir");

// 4. package up files into svn truck/ directory
echo "Copying Package into trunk...\n";
$tmp_repo_trunk_dir = $tmp_repo_dir . "/trunk";
exec("rm -rf $tmp_repo_trunk_dir");
exec("unzip $zip_of_updated_code -d $tmp_repo_trunk_dir");

// 6. copy assets/ to (svn)/assets
echo "Copying Assets...\n";
$wp_assets_dir = './wp-repo/assets';
exec("rm -rf $tmp_repo_assets_dir"); // remove assets dir in repo
exec("rsync -avC $wp_assets_dir $tmp_repo_dir");// copy excluding `.` prefixed and repo dirs

// 7. review repo
echo "Review repo. Looking good? (y/n)\n";
exec("open $tmp_repo_dir");
$get_continue = strtolower(trim( fgets( STDIN ))) == 'y';
if(!$get_continue) die;

// 7. commit files with message
chdir($tmp_repo_dir);
echo "Commiting to SVN & Uploading...\n";
exec("svn add --force * --auto-props --parents --depth infinity -q"); // force add to svn working copy
exec("svn ci -m 'Release version $version' --username $user --password $pass");

chdir($plugin_root);

// 8. Add tag to git and push
echo "Adding version tag to git & pushing...\n";
exec("git push");
exec("git tag $version");
exec("git push --tags");

// 9. Cleanup
echo "Cleanup...\n";
exec("rm $zip_of_updated_code");
exec("rm -rf $tmp_repo_dir");

echo "\nComplete. SVN should be updated!\n";

exec("open https://wordpress.org/plugins/food-truck/");

die;

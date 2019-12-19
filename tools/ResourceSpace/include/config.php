<?php
###############################
## ResourceSpace
## Local Configuration Script
###############################

# All custom settings should be entered in this file.
# Options may be copied from config.default.php and configured here.

# MySQL database settings
$mysql_server = 'localhost';
$mysql_username = 'root';
$mysql_password = 'root';
$mysql_db = 'resourcespace';

$mysql_bin_path = '/Applications/MAMP/Library/bin';

# Base URL of the installation
$baseurl = 'http://localhost/resourcespace';

# Email settings
$email_from = 'ruud.altenburg@naturalis.nl';
$email_notify = 'ruud.altenburg@naturalis.nl';

$spider_password = 'SA5YHArYBymE';
$scramble_key = 'PUnyVadyjaNY';


# Paths

#Design Changes
$slimheader=true;



/*

New Installation Defaults
-------------------------

The following configuration options are set for new installations only.
This provides a mechanism for enabling new features for new installations without affecting existing installations (as would occur with changes to config.default.php)

*/
                                
$thumbs_display_fields = array(8,3);
$list_display_fields = array(8,3,12);
$sort_fields = array(12);

// Set imagemagick default for new installs to expect the newer version with the sRGB bug fixed.
$imagemagick_colorspace = "sRGB";

$slideshow_big=true;
$home_slideshow_width=1400;
$home_slideshow_height=900;

$homeanim_folder="gfx/homeanim";



# ---- Paths to various external utilities ----

# If using ImageMagick/GraphicsMagick, uncomment and set next 2 lines
$imagemagick_path='/opt/local/bin';
$ghostscript_path='/opt/local/bin';
$ghostscript_executable='gs';

# If using FFMpeg to generate video thumbs and previews, uncomment and set next line.
$ffmpeg_path='/Applications/Burn.app/Contents/Resources';

# Install Exiftool and set this path to enable metadata-writing when resources are downloaded
$exiftool_path='/Applications/GraphicConverter 6.app/Contents/Frameworks';

# Path to Antiword - for text extraction / indexing of Microsoft Word Document (.doc) files
# $antiword_path='/usr/bin';

# Path to pdftotext - part of the XPDF project, see http://www.foolabs.com/xpdf/
# Enables extraction of text from PDF files
# $pdftotext_path='/usr/bin';

# Path to blender
# $blender_path='/usr/bin/';

# Path to an archiver utility - uncomment and set the lines below if download of collections is enabled ($collection_download = true)
# Example given for Linux with the zip utility:
# $archiver_path = '/usr/bin';
# $archiver_executable = 'zip';
# $archiver_listfile_argument = "-@ <";

# Example given for Linux with the 7z utility:
# $archiver_path = '/usr/bin';
# $archiver_executable = '7z';
# $archiver_listfile_argument = "@";

# Example given for Windows with the 7z utility:
# $archiver_path = 'C:\Program\7-Zip';
# $archiver_executable = '7z.exe';
# $archiver_listfile_argument = "@";


$view_title_field=51; 
$thumbs_display_fields=array(51,3);
$list_display_fields=array(51,3);


$enable_remote_apis=true;
$api_scramble_key = 'sadE5ytuPUMe';

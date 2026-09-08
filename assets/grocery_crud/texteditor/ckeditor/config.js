/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	//config.extraPlugins = 'imagebrowser';
	//config.uploadUrl = '/uploader/upload.php';
	//config.imageUploadUrl = '/uploader/upload.php?type=Images';
	
	config.extraPlugins = 'imagebrowser';
	config.imageBrowser_listUrl = "/ckeditor-imagebrowser/demo/images/images_list.json";

};

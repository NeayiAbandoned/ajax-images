<?php
/*
	Ajax Images by Bertrand Gorge, Neayi
	https://neayi.com/

	File: qa-plugin/ajax-images/qa-plugin.php
	Description: Plugin setup

	MIT License

	Copyright (c) 2019 neayi

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

*/

/*
	Plugin Name: Ajax Image
	Plugin URI: https://github.com/neayi/ajax-images
	Plugin Description: Allows to attach images to a question, resizing them on the browser (nice for phones), and uploading them in ajax.
	Plugin Version: 1.0.0
	Plugin Date: 2019-07-26
	Plugin Author: Neayi
	Plugin Author URI: https://github.com/neayi/
	Plugin License: MIT
	Plugin Minimum Question2Answer Version: 1.8
	Plugin Update Check URI:
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// Register a layer that will add the File Input and JavaScript to the edit page
qa_register_plugin_layer('qa-ai-layer.php', 'Ajax Images');

// Register a process in order to catch the ajax request that we use to store the images on the server
qa_register_plugin_module('process', 'qa-ai-process.php', 'qa_ai_process', 'Ajax Images');

// Register a filter in order to save the blobIds of each image uploaded with the question
qa_register_plugin_module('filter', 'qa-ai-filter.php', 'qa_ai_filter', 'Ajax Images');

// Register an event in order to actually save the blobids in the DB. Note that unused blobs will be deleted
qa_register_plugin_module('event', 'qa-ai-event.php', 'qa_ai_event', 'Ajax Images');

qa_register_plugin_phrases( 'lang/ajax-images-lang-*.php', 'ajax-images' );
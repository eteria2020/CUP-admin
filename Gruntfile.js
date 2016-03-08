'use strict';
module.exports = function(grunt) {

	grunt.loadNpmTasks('grunt-angular-gettext');
	grunt.initConfig({
	  nggettext_extract: {
	    pot: {
	      files: {
	        'module/Application/language/js/po/template.pot': [
	        	'module/Alarms/view/alarms/index/*.phtml',
	        	'module/CallCenter/view/call-center/index/*.phtml',
	        	'module/Alarms/public/assets-modules/alarms/js/controllers/main.js',
	        	'module/CallCenter/public/assets-modules/call-center/js/controllers/main.js'
	        ]
	      }
	    },
	  },
	  nggettext_compile: {
	    all: {
	      files: {
	        'module/Application/language/js/translations.js': ['module/Application/language/js/po/*.po']
	      }
	    },
	  },
	});
}
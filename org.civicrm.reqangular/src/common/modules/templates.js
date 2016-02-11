define(['common/angular'], function(angular) { 'use strict'; return angular.module("common.templates", []).run(["$templateCache", function($templateCache) {$templateCache.put("dialog.html","<div class=\"modal-header\">\n    <button type=\"button\" class=\"close\" ng-click=\"cancel()\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>\n    <h2 class=\"modal-title\">{{title}}</h2>\n</div>\n<div class=\"modal-body\">\n    {{msg}}\n</div>\n<div class=\"modal-footer\">\n    <button type=\"button\" class=\"btn btn-secondary-outline text-uppercase\" ng-click=\"cancel()\">{{copyCancel}}</button>\n    <button type=\"button\" class=\"btn {{classConfirm}} text-uppercase\" ng-click=\"confirm()\">{{copyConfirm}}</button>\n</div>\n");
$templateCache.put("loading.html","<div>\n    <div class=\"crm_spinner\" ng-show=\"!show\">\n        <span class=\"crm_spinner__img\"></span>\n    </div>\n    <div ng-transclude ng-show=\"show\"></div>\n</div>\n");}]);});
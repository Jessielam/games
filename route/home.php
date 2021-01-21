<?php

Route::group('home', function() {
    Route::rule('index', 'home/index/index', 'get');
});
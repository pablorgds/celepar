<?php

Route::get('auth/login/{loginWithCertificate?}', '\Celepar\Light\CentralSeguranca\CentralSegurancaController@login');
Route::get('logout', '\Celepar\Light\CentralSeguranca\CentralSegurancaController@logout');
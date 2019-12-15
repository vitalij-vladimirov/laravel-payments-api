<?php

Route::post('/transaction', 'TransactionController@setTransaction');
Route::put('/transaction', 'TransactionController@submitTransaction');
Route::get('/transaction/{transactionId}', 'TransactionController@getTransaction');

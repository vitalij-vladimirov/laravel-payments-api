<?php

Route::post('/transaction', 'TransactionController@createTransaction');
Route::post('/transaction/{transactionId}/confirm', 'TransactionController@submitTransaction');
Route::get('/transaction/{transactionId}', 'TransactionController@getTransaction');

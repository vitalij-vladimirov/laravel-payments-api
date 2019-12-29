<?php

Route::post('/transaction', 'TransactionController@createTransaction');
Route::post('/transaction/{transactionId}/confirm', 'TransactionController@confirmTransaction');
Route::get('/transaction/{transactionId}', 'TransactionController@getTransaction');

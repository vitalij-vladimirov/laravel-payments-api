<?php

Route::post('/transaction', 'TransactionController@setTransaction');
Route::post('/transaction/{transactionId}/confirm', 'TransactionController@submitTransaction');
Route::get('/transaction/{transactionId}', 'TransactionController@getTransaction');

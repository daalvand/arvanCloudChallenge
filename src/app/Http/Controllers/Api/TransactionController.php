<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Filters\Transaction\AmountFilter;
use App\Filters\Transaction\DateFilter;
use App\Filters\Transaction\OrderByFilter;
use App\Filters\Transaction\StatusFilter;
use App\Filters\Transaction\TypeFilter;
use App\Models\Transaction;
use App\Services\Wallet\TransactionService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Response;
use function response;

class TransactionController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Transaction::class, 'transaction');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Response
    {
        $perPage      = request('per_page', 10);
        $query        = Transaction::visible(Auth::user());
        $transactions = app(Pipeline::class)
            ->send($query)
            ->through([
                TypeFilter::class, StatusFilter::class, DateFilter::class,
                AmountFilter::class, OrderByFilter::class,
            ])
            ->thenReturn()
            ->simplePaginate($perPage);
        return response()->json($transactions);
    }

    /**
     * Display the specified resource.
     *
     * @param Transaction $transaction
     * @return Response
     */
    public function show(Transaction $transaction): Response
    {
        return response()->json(['data' => $transaction]);
    }
}

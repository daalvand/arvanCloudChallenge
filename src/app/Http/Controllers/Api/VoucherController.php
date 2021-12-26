<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\ChargeRequest;
use App\Http\Requests\Voucher\StoreRequest;
use App\Http\Requests\Voucher\UpdateRequest;
use App\Models\Voucher;
use App\Services\Voucher\VoucherService;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use function response;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Response
    {
        $perPage = request('per_page', 10);
        $vouchers = Voucher::visible(Auth::user())->latest()->simplePaginate($perPage);
        return response()->json($vouchers, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param Voucher $voucher
     * @return Response
     */
    public function show(Voucher $voucher): Response
    {
        return response()->json(['data' => $voucher], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest   $request
     * @param VoucherService $service
     * @return Response
     */
    public function store(StoreRequest $request, VoucherService $service): Response
    {
        return response()->json([
            'data' => $service->create($request->validated(), $request->user())
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * add charge to user
     * @param ChargeRequest $request
     * @param VoucherService     $service
     * @return Response
     */
    public function charge(ChargeRequest $request, VoucherService $service): Response
    {
        $service->charge($request->input('code'), Auth::user());
        return response()->json()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest  $request
     * @param Voucher        $voucher
     * @param VoucherService $service
     * @return Response
     */
    public function update(UpdateRequest $request, Voucher $voucher, VoucherService $service): Response
    {
        return response()->json([
            'data' => $service->update($voucher, $request->validated())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Voucher $voucher
     * @return Response
     */
    public function destroy(Voucher $voucher): Response
    {
        $voucher->delete();
        return response()->noContent();
    }
}

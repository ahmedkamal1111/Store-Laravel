<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class productBuyerTransactionController extends ApiController
{
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Product $product , User $buyer)
    {
        $rules=[
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request,$rules);

        if($buyer->id==$product->seller_id){
            return $this->errorResponse('the buyer must be different from the seller',409);
        }
        if(!$buyer->isVerified()){
            return $this->errorResponse('the buyer must be a verfied user',409);
        }
        if(!$product->seller->isVerified()){
            return $this->errorResponse('the seller must be a verfied user',409);
        }
        if(!$product->isAvailable()){
            return $this->errorResponse('the product must be available',409);
        }
        if($product->quantity < $request->quantity){
            return $this->errorResponse('the product does not have enough units for transactions',409);
        }

        return DB::transaction(function()use ($request,$product,$buyer){
         $product->quantity -= $request->quantity;
         $product->save();  

         $transaction = Transaction::create([
            'quantity'=>$request->quantity,
            'buyer_id'=>$buyer->id,
            'product_id'=>$product->id
         ]);
         return $this->showOne($transaction,201);
        });


    }

  
}

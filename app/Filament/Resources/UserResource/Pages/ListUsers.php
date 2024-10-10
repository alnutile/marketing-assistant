<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Domains\Accounts\AccountTypeEnum;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users'),
            'buyer' => Tab::make('Buyer')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', AccountTypeEnum::Buyer);
                }),
            'seller' => Tab::make('Seller')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', AccountTypeEnum::Seller);
                }),

            'buyer_and_seller' => Tab::make('Buyer And Seller')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', AccountTypeEnum::BuyerAndSeller);
                }),
            'system_user' => Tab::make('System User')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('account_type', AccountTypeEnum::SystemUser);
                }),
        ];
    }
}

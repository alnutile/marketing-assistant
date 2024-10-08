<?php

namespace App\Domains\Campaigns;

use App\Helpers\EnumHelperTrait;
use Filament\Support\Contracts\HasLabel;

enum ProductServiceEnum: string implements HasLabel
{
    use EnumHelperTrait;

    public function getLabel(): ?string
    {
        return str($this->value);
    }

    case PhysicalProduct = 'physical_product';
    case DigitalProduct = 'digital_product';
    case Subscription = 'subscription';
    case Service = 'service';
    case Event = 'event';
    case Course = 'course';
    case Software = 'software';
    case App = 'app';
    case Ebook = 'ebook';
    case ConsultingService = 'consulting_service';
    case MarketPlace = 'market_place';
    case NonProfit = 'non_profit';
    case PersonalBrand = 'personal_brand';
    case LocalBusiness = 'local_business';
    case Other = 'other';

}

<?php

namespace App\Domains\Campaigns;

use App\Helpers\EnumHelperTrait;

enum ProductServiceEnum: string
{
    use EnumHelperTrait;

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

<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class SeasonHelper
{
    public static function getSeasonArray($propertyID, $startDate, $endDate)
    {
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        $seasonList = new PropertySeasonList();
        $seasonList->filterByPropertyID($propertyID);
        $seasonList->filterByStatus(1);

        if ($startDate && $endDate) {
            $startDate = $dh->date('Y-m-d', strtotime($startDate));
            $endDate   = $dh->date('Y-m-d', strtotime($endDate));
            $seasonList->filterByStartEndDateBooking($startDate, $endDate);
        }
        $seasons = $seasonList->get();

        $seasonArr = [];
        foreach ($seasons as $season) {
            $seasonStartDate = $season->getSeasonStartDate();
            $seasonEndDate   = $season->getSeasonEndDate();

            $seasonArr['data'][$season->getID()]['dates']   = $dh->getDatesFromRange($seasonStartDate, $seasonEndDate);
            $seasonArr['data'][$season->getID()]['price']   = $season->getSeasonPrice();
            $seasonArr['minNightsSeason'][$season->getID()] = $season->getMinNightsSeason();
        }

        return $seasonArr;
    }

    public static function getPricePerDay($propertyID, $startDate, $endDate, $locale = '', $creditAmount = null)
    {
        $dh = Loader::helper('date');
        $ph = Loader::helper('price');

        $dateRangeArr = $dh->getDatesFromRange($startDate, $endDate);
        $returnList   = [];
        $subtotal     = 0;
        $seasonArr    = SeasonHelper::getSeasonArray($propertyID, $startDate, $endDate);


        $property = Property::getByID($propertyID);
        $vat      = Config::get('VAT_PERCENT');
        $noOfDays = count($dateRangeArr);
        foreach ($dateRangeArr as $k => $v) {
            if (is_array($seasonArr['data'])) {
                $price = $property->getPerDayPrice();
                foreach ($seasonArr['data'] as $k1 => $v1) {
                    if (in_array($v, $v1['dates'])) {
                        $price = $v1['price'];
                        break;
                    }
                }
                $subtotal     += $price;
                $returnList[] = [
                    'day'   => $v,
                    'price' => $ph->format($price, $locale, true)
                ];

            } else {
                $subtotal     += $property->getPerDayPrice();
                $returnList[] = [
                    'day'   => $v,
                    'price' => $ph->format($property->getPerDayPrice(), $locale, true)
                ];
            }
        }

        $minSeasonNight = self::getMinSeasonNights($startDate, $endDate, $property->getMinNights(), $seasonArr);

        $TourismFee = $property->getTourismFee();

        $newSubtotal = $subtotal;


        if($creditAmount){
            if($creditAmount && $creditAmount >= $subtotal) {
                $creditAmount = $subtotal;
                $newSubtotal = 0;
            } else if($creditAmount && $creditAmount < $subtotal) {
                $newSubtotal = $subtotal-$creditAmount;
            }

        }

        $vat_amount = ($newSubtotal * $vat / 100);
        $dhiram_fee = ($TourismFee * $noOfDays);
        $total      = $newSubtotal + $vat_amount + $dhiram_fee;

        $retArr                   = [];
        $retArr['subtotal']       = $subtotal;
        $retArr['totalVal']       = $total;
        $retArr['pricePerDay']    = $returnList;
        $retArr['VAT']            = $ph->format($vat_amount);
        $retArr['noOfDays']       = $noOfDays;
        $retArr['tourismFee']     = $ph->format($TourismFee * $noOfDays);
        $retArr['total']          = $ph->format($total);
        $retArr['minSeasonNight'] = $minSeasonNight;
        $retArr['creditAmount']   = $creditAmount;

        return $retArr;
    }

    protected static function getMinSeasonNights($startDate, $endDate, $propertyMiNights, $seasonArr)
    {
        if (is_array($seasonArr['data'])) {
            $startSeason = reset($seasonArr['data'])['dates'];
            $endSeason   = last($seasonArr['data'])['dates'];
            if (strtotime($startDate) < strtotime(reset($startSeason)) && strtotime($endDate) > strtotime(last($endSeason))) {
                return $propertyMiNights;
            }
        }
        return is_array($seasonArr['minNightsSeason']) ? max($seasonArr['minNightsSeason']) : 0;
    }
}

<?

class Helper_TimeZone
{
    public static function getTimeZoneSelect($selectedZone = NULL)
    {
        $regions = array(
            "Africa" => DateTimeZone::AFRICA,
            "America" => DateTimeZone::AMERICA,
            "Antarctica" => DateTimeZone::ANTARCTICA,
            "Aisa" => DateTimeZone::ASIA,
            "Atlantic" => DateTimeZone::ATLANTIC,
            "Europe" => DateTimeZone::EUROPE,
            "Indian" => DateTimeZone::INDIAN,
            "Pacific" => DateTimeZone::PACIFIC
        );

        $structure = array();
        /*$structure = '<select name="time_zone">';
        $structure .= '<option value="">Choose timezone</option>';*/

        foreach ($regions as $mask)
        {
            $zones = DateTimeZone::listIdentifiers($mask);
            $zones = self::prepareZones($zones);

            foreach ($zones as $zone)
            {
                $continent = $zone["continent"];
                $city = $zone["city"];
                $subcity = $zone["subcity"];
                $p = $zone["p"];
                $timeZone = $zone["time_zone"];
                if ($subcity)
                {
                    $city = $city . '/' . $subcity;
                }
                $structure[$continent][] = array(
                    "name" => str_replace('_', ' ', $city),
                    "utc" => $p,
                    "timezone" => $timeZone,
                    "selected" => $timeZone == $selectedZone
                );
                /*if (!isset($selectContinent))
                {
                    $structure .= '<optgroup label="' . $continent . '">';
                }
                elseif ($selectContinent != $continent)
                {
                    $structure .= '</optgroup><optgroup label="' . $continent . '">';
                }
                if ($city)
                {
                    if ($subcity)
                    {
                        $city = $city . '/' . $subcity;
                    }
                    $structure .= "<option " . (($timeZone == $selectedZone) ? 'selected="selected "' : '') . " value=\"" . ($timeZone) . "\">(" . $p . " UTC) " . str_replace('_', ' ', $city) . "</option>";
                }
                $selectContinent = $continent;*/
            }
        }

        /*$structure .= '</optgroup></select>';*/

        return $structure;
    }

    private static function prepareZones(array $timeZones)
    {
        $list = array();
        foreach ($timeZones as $zone)
        {
            $time = new DateTime(NULL, new DateTimeZone($zone));
            $p = $time->format('P');
            if ($p > 13)
            {
                continue;
            }
            $parts = explode('/', $zone);

            $list[$time->format('P')][] = array(
                'time_zone' => $zone,
                'continent' => isset($parts[0]) ? $parts[0] : '',
                'city' => isset($parts[1]) ? $parts[1] : '',
                'subcity' => isset($parts[2]) ? $parts[2] : '',
                'p' => $p,
            );
        }

        ksort($list, SORT_NUMERIC);

        $zones = array();
        foreach ($list as $grouped)
        {
            $zones = array_merge($zones, $grouped);
        }

        return $zones;
    }
}
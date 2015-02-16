FIS_TYPE fis_defuzz_smallest_of_max(FIS_TYPE** fuzzyRuleSet, int o)
{
    FIS_TYPE step = (fis_gOMax[o] - fis_gOMin[o]) / (FIS_RESOLUSION - 1);
    FIS_TYPE max = 0, dist, value;
    FIS_TYPE min = fis_gOMax[o];
    int i, midx = 0;

    for (i = 0; i < FIS_RESOLUSION; ++i)
    {
        dist = fis_gOMin[o] + (step * i);
        value = fis_MF_out(fuzzyRuleSet, dist, o);
        max = max(max, value);
    }

    for (i = 0; i < FIS_RESOLUSION; ++i)
    {
        dist = fis_gOMin[o] + (step * i);
        value = fis_MF_out(fuzzyRuleSet, dist, o);
        if (max == value)
        {
            dist = abs(dist);
            if (min > dist)
            {
                min = dist;
                midx = i;
            }
        }
    }

    return (fis_gOMin[o] + (step * midx));
}
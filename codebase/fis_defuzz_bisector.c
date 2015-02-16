FIS_TYPE fis_defuzz_bisector(FIS_TYPE** fuzzyRuleSet, int o)
{
    FIS_TYPE step = (fis_gOMax[o] - fis_gOMin[o]) / (FIS_RESOLUSION - 1);
    FIS_TYPE dist, area = 0;
    int i;

    /* find the total area */
    area = 0;
    for (i = 0; i < FIS_RESOLUSION; ++i)
    {
        dist = fis_gOMin[o] + (step * i);
        area += step * fis_MF_out(fuzzyRuleSet, dist, o);
    }
    if (area == 0)
    {
        return ((fis_gOMax[o] + fis_gOMin[o]) / 2);
    }

    area = area / 2;
    FIS_TYPE area2 = 0;
    for (i = 0; area2<area; ++i)
    {
        dist = fis_gOMin[o] + (step * i);
        area2 += step * fis_MF_out(fuzzyRuleSet, dist, o);
    }
    return (dist);
}
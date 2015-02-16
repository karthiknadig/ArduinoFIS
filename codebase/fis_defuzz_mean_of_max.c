FIS_TYPE fis_defuzz_mean_of_max(FIS_TYPE** fuzzyRuleSet, int o)
{
    FIS_TYPE step = (fis_gOMax[o] - fis_gOMin[o]) / (FIS_RESOLUSION - 1);
    FIS_TYPE max = 0, dist, value, sum;
    int count;
    int i;

    for (i = 0; i < FIS_RESOLUSION; ++i)
    {
        dist = fis_gOMin[o] + (step * i);
        value = fis_MF_out(fuzzyRuleSet, dist, o);
        max = max(max, value);
    }

    sum = 0;
    count = 0;
    for (i = 0; i < FIS_RESOLUSION; i++)
    {
        dist = fis_gOMin[o] + (step * i);
        value = fis_MF_out(fuzzyRuleSet, dist, o);
        if (max == value)
        {
            ++count;
            sum += i;
        }
    }

    return (fis_gOMin[o] + ((step * sum) / count));
}
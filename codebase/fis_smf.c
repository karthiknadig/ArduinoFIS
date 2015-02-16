// S-Shaped membership function
FIS_TYPE fis_smf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE a = p[0], b = p[1];
    FIS_TYPE m = ((a + b) / 2.0);
    FIS_TYPE t = (b - a);
    if (a >= b) return (FIS_TYPE) (x >= m);
    if (x <= a) return (FIS_TYPE) 0;
    if (x <= m)
    {
        t = (x - a) / t;
        return (FIS_TYPE) (2.0 * t * t);
    }
    if (x <= b)
    {
        t = (b - x) / t;
        return (FIS_TYPE) (1.0 - (2.0 * t * t));
    }
    return (FIS_TYPE) 1;
}
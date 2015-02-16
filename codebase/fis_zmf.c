// Z-shaped Member Function
FIS_TYPE fis_zmf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE a = p[0], b = p[1];
    FIS_TYPE m = ((a + b) / 2.0);
    FIS_TYPE t = (b - a);
    if (x <= a) return (FIS_TYPE) 1;
    if (x <= m)
    {
        t = (x - a) / t;
        return (FIS_TYPE) (1.0 - (2.0 * t * t));
    }
    if (x <= b)
    {
        t = (b - x) / t;
        return (FIS_TYPE) (1.0 - (2.0 * t * t));
    }
    return (FIS_TYPE) 0;
}

// Gaussian Member Function
FIS_TYPE fis_gauss2mf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE c1 = p[1], c2 = p[3];
    FIS_TYPE t1 = ((x >= c1) ? 1.0 : fis_gaussmf(x, p));
    FIS_TYPE t2 = ((x <= c2) ? 1.0 : fis_gaussmf(x, p + 2));
    return (t1*t2);
}
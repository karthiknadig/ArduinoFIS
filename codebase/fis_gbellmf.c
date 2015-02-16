// Generalized Bell Member Function
FIS_TYPE fis_gbellmf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE a = p[0], b = p[1], c = p[2];
    FIS_TYPE t = (x - c) / a;
    if ((t == 0) && (b == 0)) return (FIS_TYPE) 0.5;
    if ((t == 0) && (b < 0)) return (FIS_TYPE) 0;
    return (FIS_TYPE) (1.0 / (1.0 + pow(t, b)));
}
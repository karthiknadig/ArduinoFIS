// Sigmoid Member Function
FIS_TYPE fis_sigmf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE a = p[0], c = p[1];
    return (FIS_TYPE) (1.0 / (1.0 + exp(-a *(x - c))));
}
// Double Sigmoid Member Function
FIS_TYPE fis_dsigmf(FIS_TYPE x, FIS_TYPE* p)
{
    FIS_TYPE t = (fis_sigmf(x, p) - fis_sigmf(x, p + 2));
    return abs(t);
}
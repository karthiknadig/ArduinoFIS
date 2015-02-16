// Product of Sigmoid Member Function
FIS_TYPE fis_psigmf(FIS_TYPE x, FIS_TYPE* p)
{
    return (fis_sigmf(x, p) * fis_sigmf(x, p + 2));
}
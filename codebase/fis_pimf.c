// Pi-shaped Member Function
FIS_TYPE fis_pimf(FIS_TYPE x, FIS_TYPE* p)
{
    return (fis_smf(x, p) * fis_zmf(x, p + 2));
}
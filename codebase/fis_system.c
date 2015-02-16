//***********************************************************************
// Matlab .fis to arduino C converter v/*FIS_Version*/                              
// - Karthik Nadig, USA                                                  
// Please report bugs to: karthiknadig@gmail.com                         
//***********************************************************************

#include "/*FIS_HeaderFile*/"

// Number of inputs to the fuzzy inference system
const int fis_gcI = /*FIS_InputCount*/;
// Number of outputs to the fuzzy inference system
const int fis_gcO = /*FIS_OutputCount*/;
// Number of rules to the fuzzy inference system
const int fis_gcR = /*FIS_RulesCount*/;

FIS_TYPE g_fisInput[fis_gcI];
FIS_TYPE g_fisOutput[fis_gcO];

// Setup routine runs once when you press reset:
void setup()
{
    // initialize the Analog pins for input.
/*PinModeSetup_Input*/

    // initialize the Analog pins for output.
/*PinModeSetup_Output*/
}

// Loop routine runs over and over again forever:
void loop()
{
/*AnalogInput_Read*/
/*AnalogOutput_Reset*/
    fis_evaluate();

/*AnalogOutput_Write*/
}

//***********************************************************************
// Support functions for Fuzzy Inference System                          
//***********************************************************************
/*FIS_SupportFunctions*/

//***********************************************************************
// Data for Fuzzy Inference System                                       
//***********************************************************************
// Pointers to the implementations of member functions
_FIS_MF fis_gMF[] =
{
/*FIS_MFImplementations*/
};

// Count of member function for each Input
int fis_gIMFCount[] = {/*FIS_IMFCounts*/};

// Count of member function for each Output 
int fis_gOMFCount[] = {/*FIS_OMFCounts*/};

// Coefficients for the Input Member Functions
/*FIS_MFInputsCoeffs*/
FIS_TYPE** fis_gMFICoeff[] = {/*FIS_MFInputsCoeffsList*/};

// Coefficients for the Input Member Functions
/*FIS_MFOutputsCoeffs*/
FIS_TYPE** fis_gMFOCoeff[] = {/*FIS_MFOutputsCoeffsList*/};

// Input membership function set
/*FIS_InputMFs*/
int* fis_gMFI[] = {/*FIS_InputMFsList*/};

// Output membership function set
/*FIS_OutputMFs*/
int* fis_gMFO[] = {/*FIS_OutputMFsList*/};

// Rule Weights
FIS_TYPE fis_gRWeight[] = {/*FIS_RuleWeightsList*/};

// Rule Type
int fis_gRType[] = {/*FIS_RuleTypeList*/};

// Rule Inputs
/*FIS_RuleInputs*/
int* fis_gRI[] = {/*FIS_RuleInputList*/};

// Rule Outputs
/*FIS_RuleOutputs*/
int* fis_gRO[] = {/*FIS_RuleOutputList*/};

// Input range Min
FIS_TYPE fis_gIMin[] = {/*FIS_InputMinList*/};

// Input range Max
FIS_TYPE fis_gIMax[] = {/*FIS_InputMaxList*/};

// Output range Min
FIS_TYPE fis_gOMin[] = {/*FIS_OutputMinList*/};

// Output range Max
FIS_TYPE fis_gOMax[] = {/*FIS_OutputMaxList*/};

//***********************************************************************
// Data dependent support functions for Fuzzy Inference System                          
//***********************************************************************
/*FIS_DataDependentSupportFunctions*/

//***********************************************************************
// Fuzzy Inference System                                                
//***********************************************************************
void fis_evaluate()
{
/*FIS_FuzzyInputs*/
    FIS_TYPE* fuzzyInput[fis_gcI] = {/*FIS_FuzzyInputsList*/};
/*FIS_FuzzyOutputs*/
    FIS_TYPE* fuzzyOutput[fis_gcO] = {/*FIS_FuzzyOutputsList*/};
    FIS_TYPE fuzzyRules[fis_gcR] = { 0 };
    FIS_TYPE fuzzyFires[fis_gcR] = { 0 };
    FIS_TYPE* fuzzyRuleSet[] = { fuzzyRules, fuzzyFires };
    FIS_TYPE sW = 0;

    // Transforming input to fuzzy Input
    int i, j, r, o;
    for (i = 0; i < fis_gcI; ++i)
    {
        for (j = 0; j < fis_gIMFCount[i]; ++j)
        {
            fuzzyInput[i][j] =
                (fis_gMF[fis_gMFI[i][j]])(g_fisInput[i], fis_gMFICoeff[i][j]);
        }
    }

    int index = 0;
    for (r = 0; r < fis_gcR; ++r)
    {
        if (fis_gRType[r] == 1)
        {
            fuzzyFires[r] = FIS_MAX;
            for (i = 0; i < fis_gcI; ++i)
            {
                index = fis_gRI[r][i];
                if (index > 0)
                    fuzzyFires[r] = /*FIS_ANDOperation*/(fuzzyFires[r], fuzzyInput[i][index - 1]);
                else if (index < 0)
                    fuzzyFires[r] = /*FIS_ANDOperation*/(fuzzyFires[r], 1 - fuzzyInput[i][-index - 1]);
                else
                    fuzzyFires[r] = /*FIS_ANDOperation*/(fuzzyFires[r], 1);
            }
        }
        else
        {
            fuzzyFires[r] = FIS_MIN;
            for (i = 0; i < fis_gcI; ++i)
            {
                index = fis_gRI[r][i];
                if (index > 0)
                    fuzzyFires[r] = /*FIS_OROperation*/(fuzzyFires[r], fuzzyInput[i][index - 1]);
                else if (index < 0)
                    fuzzyFires[r] = /*FIS_OROperation*/(fuzzyFires[r], 1 - fuzzyInput[i][-index - 1]);
                else
                    fuzzyFires[r] = /*FIS_OROperation*/(fuzzyFires[r], 0);
            }
        }

        fuzzyFires[r] = fis_gRWeight[r] * fuzzyFires[r];
        sW += fuzzyFires[r];
    }

    if (sW == 0)
    {
        for (o = 0; o < fis_gcO; ++o)
        {
            g_fisOutput[o] = ((fis_gOMax[o] + fis_gOMin[o]) / 2);
        }
    }
    else
    {
        for (o = 0; o < fis_gcO; ++o)
        {
/*FIS_SystemImpl*/
        }
    }
}

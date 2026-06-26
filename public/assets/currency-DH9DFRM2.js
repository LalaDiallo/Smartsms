function r(e){return`${e.toLocaleString("fr-FR")} GNF`}function t(e){return e>=1e6?`${(e/1e6).toFixed(1).replace(".",",")} M GNF`:e>=1e3?`${(e/1e3).toFixed(0)} K GNF`:r(e)}export{r as a,t as f};

@php
    $logoWidth = "22px";
    $logoHeight = "28px";
    if(isset($size) && $size == 'large'){
        $logoWidth = "122px";
        $logoHeight = "128px";
    }
@endphp
<svg width="{{ $logoWidth }}" height="{{ $logoHeight }}" viewBox="0 0 22 28" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        <linearGradient x1="77.7837161%" y1="89.7930358%" x2="3.97622611%" y2="7.83303177%" id="linearGradient-1">
            <stop stop-color="#004C6F" offset="0%"></stop>
            <stop stop-color="#A258FE" offset="100%"></stop>
        </linearGradient>
        <linearGradient x1="42.6682076%" y1="102.235139%" x2="83.9936811%" y2="-28.3824864%" id="linearGradient-2">
            <stop stop-color="#00C1FF" offset="0%"></stop>
            <stop stop-color="#0053FF" offset="100%"></stop>
        </linearGradient>
        <radialGradient cx="298.035778%" cy="35.4941908%" fx="298.035778%" fy="35.4941908%" r="268.971202%" gradientTransform="translate(2.980358,0.354942),scale(1.000000,0.260870),rotate(90.000000),translate(-2.980358,-0.354942)" id="radialGradient-3">
            <stop stop-color="#FFFFFF" offset="0%"></stop>
            <stop stop-color="#DBDBDB" offset="16%"></stop>
            <stop stop-color="#808080" offset="53%"></stop>
            <stop stop-color="#000000" offset="100%"></stop>
        </radialGradient>
        <linearGradient x1="-34.2104737%" y1="165.102373%" x2="108.367446%" y2="-48.3821619%" id="linearGradient-4">
            <stop stop-color="#0090B7" offset="0%"></stop>
            <stop stop-color="#1E9DC0" offset="11%"></stop>
            <stop stop-color="#96D1E1" offset="55%"></stop>
            <stop stop-color="#E1F2F7" offset="86%"></stop>
            <stop stop-color="#FFFFFF" offset="100%"></stop>
        </linearGradient>
        <linearGradient x1="1514.52642%" y1="435.961768%" x2="843.200102%" y2="393.152628%" id="linearGradient-5">
            <stop stop-color="#0090B7" offset="0%"></stop>
            <stop stop-color="#1E9DC0" offset="11%"></stop>
            <stop stop-color="#96D1E1" offset="55%"></stop>
            <stop stop-color="#E1F2F7" offset="86%"></stop>
            <stop stop-color="#FFFFFF" offset="100%"></stop>
        </linearGradient>
    </defs>
    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g id="voyager-logo">
            <path d="M0.784005449,13.4921532 L5.43253966,9.47022562 C6.16248517,8.84179944 7.41005124,8.84179944 8.12492042,9.47902359 C13.0071377,13.8566404 15.9344579,18.8563991 16.9609045,24.5788479 C17.2812765,26.3660919 15.5927278,27.9723492 13.360175,27.9811472 L5.96021111,28 C5.65617185,23.5469721 3.95003416,19.8643947 0.630729456,16.5425339 C-0.268824732,15.6438844 -0.195955817,14.3380148 0.784005449,13.4921532 Z" id="Path" fill="url(#linearGradient-1)"></path>
            <path d="M10.6319474,5.30897213 L16.7535207,0.222471734 C17.2051971,-0.151535648 18.001131,-0.0368400509 18.2547946,0.438149324 C22.7845,8.89071616 23.4885457,17.8008187 18.7996534,26.1997778 C18.2004381,27.2719323 16.8518801,27.9725728 15.3506063,27.9763129 L6,28 C12.4011209,22.0545293 12.7078467,14.2302949 10.0780291,7.11168771 C9.83601342,6.47587516 10.0521451,5.79268834 10.6319474,5.30897213 Z" id="Path" fill="url(#linearGradient-2)"></path>
            <path d="M10.3851418,6.56279941 C10.1921484,6.0290578 10.3014992,5.43737461 10.6739677,5 C10.1253304,5.48631683 9.92354792,6.15500248 10.1556637,6.78062882 C12.8355458,14.0120588 12.5229808,21.9602995 6,28 L6.3758693,28 C12.8052125,21.8627829 13.0887631,13.8562854 10.3851418,6.56279941 Z" id="Path" fill="url(#radialGradient-3)" style="mix-blend-mode: screen;"></path>
            <path d="M6,28 C10.7826105,22.2311396 10.4261086,15.5367259 7.5082774,9.39003684 C7.37116127,9.09842412 7.85655236,8.86640182 8.14586739,9.08828107 C9.5156914,10.1426619 10.7875517,11.3013334 11.9480976,12.552133 C12.2950014,18.0547383 10.9677173,23.6980784 6,28 Z" id="Path" fill="url(#linearGradient-4)" style="mix-blend-mode: multiply;"></path>
            <path d="M6,28 C10.96438,23.6928763 12.2907724,18.0427121 11.9495827,12.5334528 C11.4658879,12.0091846 10.9547883,11.4976105 10.4231351,11 C11.7221228,16.8685036 10.1203121,24.1168608 6,28 Z" id="Path" fill="url(#linearGradient-5)" style="mix-blend-mode: multiply;"></path>
        </g>
    </g>
</svg>
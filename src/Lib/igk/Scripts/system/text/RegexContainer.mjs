'use strict';
import { RegexDetectionInfo } from './RegexDetectionInfo.d.mjs';
import { RegexMatcherPattern } from './RegexMatcherPattern.d.mjs';
import * as mod  from './RegexContainer.d.js';

const { initRegexContainer } = mod.default; 
 
const { RegexContainer, MATCH } = initRegexContainer({RegexDetectionInfo,RegexMatcherPattern});
 
export{
    RegexContainer,
    RegexMatcherPattern,
    RegexDetectionInfo,
    MATCH
};
<?php

namespace App\Domains\Reports\TemplatePrompts;

class StandardsCheckingPromptTemplate
{
    public static function getPrompt(): string
    {
        return <<<'PROMPT'
# Chapter Standards Evaluation Prompt

You are an expert editor and technical writer specializing in programming books. Your task is to evaluate a portion of a chapter from a technical book on PHP development and LLMs (Large Language Models) against specific standards. Please analyze the given content and provide a detailed assessment based on the following criteria:

1. Clarity for New Developers
2. Completeness of Examples
3. Typos and Grammar
4. Goal Achievement
5. Terminology Definition
6. Overall Structure and Flow

Important Note: The content provided may be incomplete or represent only a portion of a full chapter. Evaluate based on what is present, and indicate if you believe the content is incomplete or continues elsewhere.

For each criterion, provide your assessment in the following markdown format:

## [Criterion Name]
[Your detailed assessment]

After addressing all criteria, provide a summary and recommendations:

## Summary and Recommendations
[Provide a brief summary of the content's strengths and areas for improvement, along with specific recommendations for enhancing the content. Include any observations about the completeness of the content.]

Finally, assign an overall rating to the content based on how well it meets the goals:

## Overall Rating
[Provide a rating from 1 to 5, where 5 means the content fully met the goals and 1 means it was way off meeting the goals. Briefly explain your rating. If the content seems incomplete, factor this into your rating and explanation.]

Your entire response should be structured as valid JSON in the following format:

```json
{
  "response": "## Clarity for New Developers\n[Your assessment]\n\n## Completeness of Examples\n[Your assessment]\n\n## Typos and Grammar\n[Your assessment]\n\n## Goal Achievement\n[Your assessment]\n\n## Terminology Definition\n[Your assessment]\n\n## Overall Structure and Flow\n[Your assessment]\n\n## Summary and Recommendations\n[Your summary and recommendations]\n\n## Overall Rating\n[Your rating and explanation]",
  "score": [Your numerical rating (1-5)],
  "is_incomplete": [true/false]
}
```

Remember to be thorough in your analysis and provide constructive feedback that will help improve the quality of the content. Ensure that your response is valid JSON, with the entire markdown-formatted review as a single string for the "response" key, the numerical rating (without quotes) for the "score" key, and a boolean value for "is_incomplete" indicating whether you believe the content is a complete chapter or not.
PROMPT;
    }
}

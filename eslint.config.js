import eslint from '@eslint/js';
import globals from 'globals';
import tseslint from 'typescript-eslint';
import wceslint from 'eslint-plugin-wc';
import liteslint from 'eslint-plugin-lit';

export default [
  eslint.configs.recommended,
  ...tseslint.configs.recommended,
  //wceslint.configs.recommended,
  //liteslint.configs.recommended,
  {
    files: ['src/js/**'],
    languageOptions: {
      ecmaVersion: 'latest',
      globals: {
        ...globals.browser
      },
    },
  },
];

export const customMedia = {
  '--lte-mobile': `(max-width: 600px)`,
  '--gt-mobile': `(min-width: 601px)`,

  /* 65rem content + 2*15.625rem figure + 2*2rem gutter
   * where 15.625rem=250px figure width. Twice the figure width, because
   * we have to account for the far side, too. */
  '--gt-content-and-figure': `(min-width: 84.625rem)`,
};

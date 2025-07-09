function fixParentheses(str) {
  let openNeeded = 0;
  let balance = 0;

  for (let char of str) {
    if (char === '(') {
      balance++;
    } else if (char === ')') {
      if (balance > 0) {
        balance--;
      } else {
        openNeeded++;
      }
    }
  }

  let closeNeeded = balance;
  return '('.repeat(openNeeded) + str + ')'.repeat(closeNeeded);
}

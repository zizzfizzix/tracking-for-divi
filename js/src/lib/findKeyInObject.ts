export const findKeyInObject = (object: Record<string, any>, query: string) =>
  Object.keys(object).filter((key) => key.startsWith(query))[0];

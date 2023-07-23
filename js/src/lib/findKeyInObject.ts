// The form payload keys include a suffix form ID which is an unknown so we need to find the correct keys based on the known format
export const findKeyInObject = (object: Record<string, any>, query: string) =>
  Object.keys(object).filter((key) => key.startsWith(query))[0];

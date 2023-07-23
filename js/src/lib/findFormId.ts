// The form payload keys include a form ID suffix which is a "0" by default, but we define it just in case.
export const findFormId = (objKey: string) =>
  /.+_([^_]+)/.exec(objKey)?.[1] ?? "0";

export const findFormId = (objKey: string) =>
  /.+_([^_]+)/.exec(objKey)?.[1] ?? "0";

// flow-typed signature: ce9460b02a3ccf3df34711037f718704
// flow-typed version: <<STUB>>/react-dropzone_v5/flow_v0.75.0

import * as React from 'react';
import type { HTMLAttributes } from 'react';

declare module "react-dropzone" {
  declare export function useDropzone(options?: DropzoneOptions): DropzoneState;

  declare export { FileWithPath } from "file-selector";

  declare export type FileError ={
    message: string;
    code: "file-too-large" | "file-too-small" | "too-many-files" | "file-invalid-type" | string;
  }

  declare export type FileRejection= {
    file: File;
    errors: FileError[];
  }
 
  declare export type DropzoneFile = File & {
    preview?: string;
  }
  
  declare export default function Dropzone(
    props: DropzoneProps
  ): JSX.Element;

  declare export type DropzoneProps = {
    children?: (state: DropzoneState) => JSX.Element
  } & DropzoneOptions;

  declare export type DropzoneOptions = Pick<React.HTMLProps<HTMLElement>, PropTypes> & {
    accept?: string | string[],
    minSize?: number,
    maxSize?: number,
    preventDropOnDocument?: boolean,
    noClick?: boolean,
    noKeyboard?: boolean,
    noDrag?: boolean,
    noDragEventsBubbling?: boolean,
    disabled?: boolean,
    onDrop?: (acceptedFiles: Array<DropzoneFile>, fileRejections: FileRejection[], event: SyntheticDragEvent<>) => mixed,
    onDropAccepted?: (files: Array<DropzoneFile>, event: SyntheticDragEvent<>) => mixed,
    onDropRejected?: (fileRejections: FileRejection[], event: SyntheticDragEvent<>) => mixed,
    getFilesFromEvent?: (
      event: DropEvent
    ) => Promise<Array<File | DataTransferItem>>,
    onFileDialogCancel?: () => void,
    maxFiles?: number,
    validator?: (file:File) => FileError | FileError[] | null,

  };

  declare export type DropEvent =
    | React.DragEvent<HTMLElement>
    | React.ChangeEvent<HTMLInputElement>
    | DragEvent
    | Event;

  declare export type DropzoneState = DropzoneRef & {
    isFocused: boolean,
    isDragActive: boolean,
    isDragAccept: boolean,
    isDragReject: boolean,
    isFileDialogActive: boolean,
    draggedFiles: File[],
    acceptedFiles: File[],
    fileRejections: FileRejection[];
    rootRef: React.RefObject<HTMLElement>,
    inputRef: React.RefObject<HTMLInputElement>,
    getRootProps(props?: DropzoneRootProps): DropzoneRootProps,
    getInputProps(props?: DropzoneInputProps): DropzoneInputProps
  };

  declare export interface DropzoneRef {
    open(): void;
    getRootProps(props?: DropzoneRootProps): DropzoneRootProps,
    getInputProps(props?: DropzoneInputProps): DropzoneInputProps
  }

  declare export type DropzoneRootProps = {
    refKey?: string,
    [key: string]: any
  } & HTMLAttributes;

  declare export type DropzoneInputProps = {
    refKey?: string
  } & React.InputHTMLAttributes;

  declare type PropTypes =
    | "multiple"
    | "onDragEnter"
    | "onDragOver"
    | "onDragLeave";
}

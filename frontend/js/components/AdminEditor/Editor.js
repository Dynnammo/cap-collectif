// @flow
import React, { useState, useEffect } from 'react';
import { injectIntl, type IntlShape } from 'react-intl';

import useIsMounted from './hooks/useIsMounted';
import useToggle from './hooks/useToggle';
import * as Icons from './components/Icons';
import CodeEditor from './CodeEditor';
import { EditorWrapper, NotificationBanner } from './Editor.style';

type Props = {
  intl: IntlShape,
  id?: string,
  name: string,
  /** must be HTML format */
  initialContent: string,
  onContentChange?: (string, {| html: string, raw: ?Object |}) => void,
};

function Editor({ id = '', name, initialContent = '', onContentChange = () => {}, intl }: Props) {
  const { current: isMounted } = useIsMounted();
  const [editorMode, setEditorMode] = useState<'wysiwyg' | 'code' | null>(null);
  const [notification, setNotification] = useState<?{ type: string, message: string }>(null);
  const [fullscreen, toggleFullscreen] = useToggle(false);
  const [htmlSource, setHtmlSource] = useState<string>(initialContent); // html state

  function toggleEditorMode() {
    setEditorMode(state => (state === 'wysiwyg' ? 'code' : 'wysiwyg'));
  }

  // Side-effect to detect editor mode
  useEffect(() => {
    // TODO: improve detection of pure html on starting
    if (/ class=/i.test(initialContent) || / id=/i.test(initialContent)) {
      setNotification({
        type: 'info',
        message: intl.formatMessage({ id: 'editor.notification.autoconvert' }),
      });
      setEditorMode('code');
    } else {
      setEditorMode('wysiwyg');
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Side-effect to disappear notification after 10s
  useEffect(() => {
    setTimeout(() => {
      setNotification(null);
    }, 10000);
  }, [notification]);

  // Side-effect to keep CodeEditor sync
  useEffect(() => {
    if (editorMode === 'code') {
      onContentChange(name, { html: htmlSource, raw: null });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [editorMode, htmlSource]);

  if (!isMounted) return null;

  return (
    <EditorWrapper id={id} fullscreen={fullscreen}>
      <CodeEditor
        content={htmlSource}
        onChange={setHtmlSource}
        toggleFullscreen={toggleFullscreen}
        toggleEditorMode={toggleEditorMode}
        fullscreen={fullscreen}
      />
      {notification && (
        <NotificationBanner>
          <Icons.Info width="1em" height="1em" viewBox="0 0 24 24" /> {notification.message}
        </NotificationBanner>
      )}
    </EditorWrapper>
  );
}

export default injectIntl(Editor);

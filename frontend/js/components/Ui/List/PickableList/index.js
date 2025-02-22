// @flow
import * as React from 'react';
import InfiniteScroll from 'react-infinite-scroller';
import { type StyledComponent } from 'styled-components';
import * as S from './styles';
import Header from './header';
import Body from './body';
import Row from './row';
import { type Action, createReducer, type State } from '~ui/List/PickableList/reducer';
import { PickableListContext } from '~ui/List/PickableList/context';

type Props = {
  isLoading?: boolean,
  onScrollToBottom?: () => void,
  useInfiniteScroll?: boolean,
  hasMore?: boolean,
  loader?: React.Node,
  children: React.ChildrenArray<
    | React.Element<typeof Header>
    | React.Element<typeof Body>
    | React.Element<StyledComponent<{}, {}, typeof Body>>
    | React.Element<StyledComponent<{}, {}, typeof Header>>,
  >,
};

type ProviderProps = {|
  +children: React.Node,
|};

const Provider = ({ children }: ProviderProps) => {
  const [state, dispatch] = React.useReducer<State, Action>(createReducer, { rows: {} });
  const context = React.useMemo(
    () => ({
      get hasAnyRowsChecked() {
        return Object.keys(state.rows).some(rowId => state.rows[rowId] === true);
      },
      get hasAllRowsChecked() {
        const keys = Object.keys(state.rows);
        return keys.length > 0 && keys.every(rowId => state.rows[rowId] === true);
      },
      get selectedRows() {
        return Object.keys(state.rows).filter(rowId => state.rows[rowId] === true);
      },
      get hasIndeterminateState() {
        return this.hasAnyRowsChecked && this.selectedRows.length < Object.keys(this.rows).length;
      },
      get rowsCount() {
        return Object.keys(this.rows).length;
      },
      isRowChecked: rowId => rowId in state.rows && state.rows[rowId] === true,
      rows: state.rows,
      dispatch,
    }),
    [state.rows],
  );
  return <PickableListContext.Provider value={context}>{children}</PickableListContext.Provider>;
};

const noop = () => {};

const PickableList = ({
  children,
  loader,
  useInfiniteScroll = false,
  onScrollToBottom = noop,
  hasMore = true,
  isLoading = false,
  ...rest
}: Props) => {
  return (
    <S.Container {...rest} isLoading={isLoading}>
      {isLoading && (
        <S.GlobalLoaderContainer>
          <S.GlobalLoader />
        </S.GlobalLoaderContainer>
      )}
      {useInfiniteScroll ? (
        <InfiniteScroll
          initialLoad={false}
          pageStart={0}
          loadMore={onScrollToBottom}
          hasMore={hasMore}
          loader={loader}>
          {children}
        </InfiniteScroll>
      ) : (
        children
      )}
    </S.Container>
  );
};

PickableList.Provider = Provider;
PickableList.Header = Header;
PickableList.Body = Body;
PickableList.Row = Row;

export default PickableList;

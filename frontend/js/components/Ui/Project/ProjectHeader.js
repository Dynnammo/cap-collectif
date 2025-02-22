// @flow
import * as React from 'react';
import {Flex} from '@cap-collectif/ui';
import { type AppBoxProps } from '~ui/Primitives/AppBox.type';

import {
  Content,
  Cover,
  Authors,
  Block,
  Blocks,
  CoverImage,
  CoverVideo,
  Social,
  Socials,
  Title,
  Info,
} from './ProjectHeader.Cover';
import { Frise, Step, Steps } from './ProjectHeader.Frise';

type Props = {|
  ...AppBoxProps,
  children?: React.Node,
|};

const ProjectHeader = ({ children, ...rest }: Props) => {
  return (
    <Flex direction="column" className="projectHeader" maxWidth="100%" paddingY={[0, 9]} {...rest}>
      {children}
    </Flex>
  );
};
ProjectHeader.Cover = Cover;
ProjectHeader.Frise = Frise;
ProjectHeader.Title = Title;
ProjectHeader.Content = Content;
ProjectHeader.CoverImage = CoverImage;
ProjectHeader.CoverVideo = CoverVideo;
ProjectHeader.Authors = Authors;
ProjectHeader.Blocks = Blocks;
ProjectHeader.Block = Block;
ProjectHeader.Info = Info;
ProjectHeader.Socials = Socials;
ProjectHeader.Social = Social;
ProjectHeader.Steps = Steps;
ProjectHeader.Step = Step;

export default ProjectHeader;

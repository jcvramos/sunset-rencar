import React from 'react';
import { Composition } from 'remotion';
import { HeroAnimation } from './HeroAnimation';

export const Root: React.FC = () => {
  return (
    <>
      <Composition
        id="HeroSunset"
        component={HeroAnimation}
        durationInFrames={150}
        fps={30}
        width={1920}
        height={1080}
        defaultProps={{}}
      />
    </>
  );
};

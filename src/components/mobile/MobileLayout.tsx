import { ReactNode } from "react";

interface MobileLayoutProps {
  children: ReactNode;
  className?: string;
}

export function MobileLayout({ children, className }: MobileLayoutProps) {
  return (
    <div className="min-h-screen max-w-md mx-auto bg-background relative overflow-hidden">
      <div className={className}>
        {children}
      </div>
    </div>
  );
}

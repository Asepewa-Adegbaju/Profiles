import { Button } from "@/components/Button";
import { Menu } from "lucide-react"; 
import {useState} from "react";
import { X} from "lucide-react";

const navLinks = [
  { href: "#about", label: "About" },
  { href: "#projects", label: "Projects" },
  { href: "#experience", label: "Experience" },
  { href: "#testimonials", label: "Testimonials" },
];

export const Navbar = () => {
    const [isMobileMenuOpen, setIsMobileMenuOpen] =useState(false)
  return (
    <header className="fixed top-0 right-0 left-0 bg-transparent py-5 z-50">
      <nav className="container mx-auto px-6 flex items-center justify-between">
        
        <a href="#" className="text-xl font-bold tracking-tight hover:text-primary transition-colors">
          JO<span className="text-primary">.</span>
        </a>

        {/* Desktop Nav */}
        <div className="hidden md:flex items-center gap-1">
          <div className="bg-surface/50 backdrop-blur-md border border-border rounded-full px-2 py-1 flex gap-1 items-center">
            {navLinks.map((link, index) => (
              <a 
                key={index} 
                href={link.href} 
                className="px-4 py-2 text-sm text-muted-foreground hover:text-foreground rounded-full hover:bg-surface transition-all"
              >
                {link.label}
              </a>
            ))}
          </div>
        </div>

        {/* Call to action button */}
        <div className="hidden md:block">
          <Button size="sm">

            <a href="#contact">Contact Me</a>
          </Button>
        </div>

        {/* Mobile nav - hamburger menu */}
        <button className="md:hidden p-2 text-foreground transition-colors hover:text-primary cursor-pointer " onClick ={()=> setIsMobileMenuOpen(!isMobileMenuOpen)}>
          {isMobileMenuOpen ? <X size={24} />: <Menu size={24} />}
        </button>

        {/* mobile menu */}
{isMobileMenuOpen && (
    <div className="md:hidden glass-strong absolute top-full left-0 right-0">
  <div className="container mx-auto px-6 py-6 flex flex-col gap-4 animate-fade-in">
    {navLinks.map((link, index) => (
      <a 
        key={index} 
        href={link.href} 
        className="text-lg text-muted-foreground hover:text-foreground py-2"
      >
        {link.label}
      </a>
    ))}
          <Button>

            <a href="#contact">Contact Me</a>
          </Button>
  </div>
</div>
)}
      </nav>
    </header>
  );
};
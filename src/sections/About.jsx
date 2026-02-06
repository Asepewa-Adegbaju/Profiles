import { Rocket, Cpu, Telescope, Handshake } from "lucide-react"

const highlights =[
    {
        icon:Rocket,
        title:"Gedreven Autodidact",
        description:"Ik legde de basis vóór mijn studie en leer mezelf nu React."
        
    },
    {
         icon:Cpu ,
        title:"Focus op Moderne Architectuur",
        description:"Ik focus op herbruikbare code met tools als React en Tailwind."
    },
    {
         icon:Telescope,
        title:"Ambiteuze Explorer Mindset",
        description:"Ik ontdek de hele stack, van strakke frontends tot PHP-logica."
    },
    {
         icon:Handshake,
        title:"Leergierig",
        description:"Ik leer snel en durf vragen te stellen om sneller tot resultaat te komen."
    }

]


export const About = () => {
    return (
    <section id="about" className="py-32 relative overflow-hidden">
        <div className="container mx-auto px-6 relative z-10">
            <div className="grid lg:grid-cols-2 gap-16 items-center">
                {/*Left */}
                <div className="space-y-8">
                    <div className="animate-fade-in">
                    <span className="text-secondary-foreground text-sm font-medium tracking-wider">
                        OVER MIJ
                        </span>
                </div>
                <h2 className="text-4xl md:text-5xl font-bold leading-tight animate-fade-in delay-100 text-secondary-foreground">Driven by code,<br />
                <span className="font-serif italic font-normal text-white"> defined by constant growth.</span>
                </h2>
                <div className="space-y-4 text-muted-foreground animate-fade-in animation-fade-in-200">
                    <p>
        Ik ben Asepewa Adegbaju, een Software Developer met een sterke focus op snelheid en tastbaar resultaat. 
        Mijn weg in de tech-wereld begon eigenlijk heel doelgericht; in de zomer van 2025 besloot ik niet te wachten op mijn opleiding, maar ben ik zelf alvast in de code gedoken. 
        Wat startte als een eerste kennismaking met de basis, is inmiddels uitgegroeid tot het bouwen van serieuze applicaties met React en PHP.
    </p>
    <p>
        Ik omschrijf mezelf graag als een Fullstack Explorer. Voor mij betekent dit dat ik nooit stilsta en altijd op zoek ben naar nieuwe technieken om mijn tech-stack sterker te maken. 
        Ik geloof erin dat je het meeste leert door gewoon te gaan bouwen en je eigen grenzen op te zoeken.
    </p>
    <p>
        Daarbij ben ik behoorlijk kritisch op mijn eigen werk; code moet voor mij niet alleen werken, maar ook schoon en logisch in elkaar zitten. 
        Mijn doel is dan ook simpel: complexe ideeën vertalen naar heldere, functionele oplossingen waar mensen echt iets aan hebben.
    </p>
                </div>
            </div>
            <div className="glass rounded-2xl p-6 animate-fade-in-300">
                <p className="text-lg font-medium italic text-foreground">
                    Mijn doel is om uit te groeien tot een developer die de complete stack beheerst.
                     Ik geloof dat stilstand achteruitgang is, en daarom zoek ik elke dag mijn grenzen op. Door zelf diep in de code te duiken en op de juiste momenten hulp te vragen bij ervaren developers, 
                    versnel ik mijn groei om sneller tot slimme, schone oplossingen te komen.
                </p>
            </div>
            </div>
            {/*right */}
            <div className="grid sm:grid-cols-2 gap-6">
                {highlights.map((item,idx) =>
                <div key={idx} className="glass p-6 rounded-2xl animate-fade-in "
                style={{animationDelay:`${(idx +1)*100}ms`}}
                >
                    <div className="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 hover:bg-primary/20">
                        <item.icon className="w-6 h-6 text-primary" />
                    </div>
                    <h3 className="text-lg font-semibold mb-2">{item.title}</h3>
                    <p className="text-sm text-muted-foreground">{item.description}</p>
                </div>
                )}
            </div>
        </div>
    </section>
    )
}
